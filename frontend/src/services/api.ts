import {
  Device,
  NotificationItem,
  Order,
  Product,
  Shift,
  ShiftMetrics,
  Table,
  ThermalReceipt,
  User,
} from '../types';
import { mockStore } from './mockStore';

const BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/$/, '');

class ApiService {
  private token: string | null = localStorage.getItem('nexus_token');

  setToken(token: string | null) {
    this.token = token;
    if (token) {
      localStorage.setItem('nexus_token', token);
    } else {
      localStorage.removeItem('nexus_token');
    }
  }

  getToken(): string | null {
    return this.token;
  }

  private async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(options.headers as Record<string, string> || {}),
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
    }

    const response = await fetch(`${BASE_URL}${endpoint}`, {
      ...options,
      headers,
    });

    if (!response.ok) {
      let errorMsg = `HTTP Error ${response.status}`;
      try {
        const errorData = await response.json();
        errorMsg = errorData.message || JSON.stringify(errorData);
      } catch {
        // use default
      }
      throw new Error(errorMsg);
    }

    return response.json();
  }

  // --- Auth ---
  async login(credentials: { email?: string; password?: string; pin?: string }): Promise<{ token: string; user: User }> {
    try {
      const data = await this.request<{ token: string; user: User }>('/auth/login', {
        method: 'POST',
        body: JSON.stringify(credentials),
      });
      this.setToken(data.token);
      return data;
    } catch (err: any) {
      // Fallback to offline / standalone mock store when API is unavailable (e.g. HTTP 405 on Vercel)
      console.warn('Backend unavailable, using local mock store for login:', err?.message);
      const res = mockStore.login(credentials);
      this.setToken(res.token);
      return res;
    }
  }

  async getCurrentUser(): Promise<{ user: User }> {
    try {
      return await this.request<{ user: User }>('/auth/user');
    } catch {
      return mockStore.getCurrentUser();
    }
  }

  async logout(): Promise<void> {
    try {
      await this.request('/auth/logout', { method: 'POST' });
    } catch {
      mockStore.logout();
    } finally {
      this.setToken(null);
    }
  }

  // --- Shifts ---
  async getCurrentShift(): Promise<{ active: boolean; shift: Shift | null; metrics: ShiftMetrics }> {
    try {
      return await this.request<{ active: boolean; shift: Shift | null; metrics: ShiftMetrics }>('/shifts/current');
    } catch {
      return mockStore.getCurrentShift();
    }
  }

  async startShift(data: { notes?: string }): Promise<{ message: string; shift: Shift }> {
    try {
      return await this.request<{ message: string; shift: Shift }>('/shifts/start', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.startShift(data);
    }
  }

  async closeShift(id: number, data: { cash_counted?: number; deductions?: number; notes?: string }): Promise<{ message: string; shift: Shift }> {
    try {
      return await this.request<{ message: string; shift: Shift }>(`/shifts/${id}/close`, {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.closeShift(id, data);
    }
  }

  async getShiftHistory(): Promise<{ shifts: Shift[] }> {
    try {
      return await this.request<{ shifts: Shift[] }>('/shifts/history');
    } catch {
      return mockStore.getShiftHistory();
    }
  }

  // --- Devices & Gaming Sessions ---
  async getDevices(): Promise<{ devices: Device[]; summary: { total_devices: number; active_devices: number; available_devices: number; maintenance_devices: number } }> {
    try {
      return await this.request<{ devices: Device[]; summary: { total_devices: number; active_devices: number; available_devices: number; maintenance_devices: number } }>('/devices');
    } catch {
      return mockStore.getDevices();
    }
  }

  async startSession(deviceId: number, data: { duration_minutes: number; customer_name?: string; customer_phone?: string; discount?: number }) {
    try {
      return await this.request(`/devices/${deviceId}/session/start`, {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.startSession(deviceId, data);
    }
  }

  async extendSession(sessionId: number, added_minutes: number) {
    try {
      return await this.request(`/sessions/${sessionId}/extend`, {
        method: 'PATCH',
        body: JSON.stringify({ added_minutes }),
      });
    } catch {
      return mockStore.extendSession(sessionId, added_minutes);
    }
  }

  async addBeverageToSession(sessionId: number, items: { product_id: number; quantity: number; notes?: string }[]) {
    try {
      return await this.request(`/sessions/${sessionId}/add-beverage`, {
        method: 'PATCH',
        body: JSON.stringify({ items }),
      });
    } catch {
      return mockStore.addBeverageToSession(sessionId, items);
    }
  }

  async endSession(sessionId: number, data: { payment_method: string; discount?: number; amount_paid?: number }) {
    try {
      return await this.request<{ message: string; receipt: ThermalReceipt }>(`/sessions/${sessionId}/end`, {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.endSession(sessionId, data);
    }
  }

  // --- POS Orders ---
  async getOrders(params: { order_type?: string; status?: string } = {}): Promise<{ data: Order[] }> {
    try {
      const query = new URLSearchParams(params as Record<string, string>).toString();
      return await this.request<{ data: Order[] }>(`/orders?${query}`);
    } catch {
      return mockStore.getOrders();
    }
  }

  async createOrder(data: {
    order_type: 'take_away' | 'dine_in' | 'gaming_room';
    table_id?: number | null;
    device_session_id?: number | null;
    items: { product_id: number; quantity: number; notes?: string }[];
    discount?: number;
    tax?: number;
    payment_method?: string;
    payment_status?: string;
    notes?: string;
  }): Promise<{ message: string; order: Order }> {
    try {
      return await this.request<{ message: string; order: Order }>('/orders', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.createOrder(data);
    }
  }

  async processOrderPayment(orderId: number, data: { payment_method: string; amount?: number }) {
    try {
      return await this.request(`/orders/${orderId}/payment`, {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch {
      return { message: 'تم تسجيل الدفع بنجاح' };
    }
  }

  async getOrderReceipt(orderId: number): Promise<{ receipt: ThermalReceipt }> {
    try {
      return await this.request<{ receipt: ThermalReceipt }>(`/orders/${orderId}/receipt`);
    } catch {
      return {
        receipt: {
          business_name: 'AL5AL Gaming & Lounge',
          business_name_ar: 'صالة الخال للألعاب والبلياردو والكافيه',
          order_number: 'ORD-REC-' + orderId,
          date_time: new Date().toLocaleString('ar-EG'),
          staff_name: 'كاشير الصالة',
          order_type: 'dine_in',
          items: [],
          subtotal: 75.00,
          discount: 0,
          tax: 0,
          total_amount: 75.00,
          payment_method: 'cash',
          payment_status: 'paid',
          footer_note: 'Thank you for visiting AL5AL!',
          footer_note_ar: 'شكراً لزيارتكم صالة الخال!',
        },
      };
    }
  }

  // --- Tables ---
  async getTables(): Promise<{ tables: Table[]; summary: { total_tables: number; occupied_tables: number; available_tables: number } }> {
    try {
      return await this.request<{ tables: Table[]; summary: { total_tables: number; occupied_tables: number; available_tables: number } }>('/tables');
    } catch {
      return mockStore.getTables();
    }
  }

  async occupyTable(tableId: number) {
    try {
      return await this.request(`/tables/${tableId}/occupy`, { method: 'PATCH' });
    } catch {
      return mockStore.occupyTable(tableId);
    }
  }

  async moveTableToGaming(tableId: number, device_session_id: number) {
    try {
      return await this.request(`/tables/${tableId}/move-to-gaming`, {
        method: 'POST',
        body: JSON.stringify({ device_session_id }),
      });
    } catch {
      return { message: 'تم نقل الطاولة للعبة بنجاح' };
    }
  }

  async releaseTable(tableId: number, payment_method: string = 'cash') {
    try {
      return await this.request(`/tables/${tableId}/release`, {
        method: 'POST',
        body: JSON.stringify({ payment_method }),
      });
    } catch {
      return mockStore.releaseTable(tableId, payment_method);
    }
  }

  // --- Products & Inventory ---
  async getProducts(params: { category?: string; search?: string } = {}): Promise<{ products: Product[]; categories: Record<string, string>; summary: { total_products: number; low_stock_count: number } }> {
    try {
      const query = new URLSearchParams(params as Record<string, string>).toString();
      return await this.request(`/products?${query}`);
    } catch {
      return mockStore.getProducts();
    }
  }

  async updateStock(productId: number, data: { quantity_change: number; reason: 'restock' | 'adjustment' | 'sale' }) {
    try {
      return await this.request(`/products/${productId}/stock`, {
        method: 'PATCH',
        body: JSON.stringify(data),
      });
    } catch {
      return mockStore.updateStock(productId, data);
    }
  }

  async getInventoryReport(): Promise<{ logs: any[]; low_stock_products: Product[] }> {
    try {
      return await this.request('/inventory/report');
    } catch {
      return { logs: [], low_stock_products: [] };
    }
  }

  // --- Notifications ---
  async getNotifications(): Promise<{ notifications: NotificationItem[]; unread_count: number }> {
    try {
      return await this.request<{ notifications: NotificationItem[]; unread_count: number }>('/notifications');
    } catch {
      return mockStore.getNotifications();
    }
  }

  async markNotificationAsRead(id: number) {
    try {
      return await this.request(`/notifications/${id}/read`, { method: 'PATCH' });
    } catch {
      return mockStore.markNotificationAsRead(id);
    }
  }

  async markAllNotificationsAsRead() {
    try {
      return await this.request('/notifications/read-all', { method: 'POST' });
    } catch {
      return mockStore.markAllNotificationsAsRead();
    }
  }

  // --- Reports ---
  async getDashboardReport(): Promise<{
    metrics: {
      total_revenue_today: number;
      cafe_revenue_today: number;
      gaming_revenue_today: number;
      cash_total: number;
      card_total: number;
      orders_count: number;
      sessions_count: number;
      active_devices_count: number;
      total_devices_count: number;
      device_occupancy_rate: number;
      occupied_tables_count: number;
      total_tables_count: number;
      table_occupancy_rate: number;
      low_stock_count: number;
    };
    current_shift: Shift | null;
    top_products: any[];
    recent_orders: Order[];
  }> {
    try {
      return await this.request('/reports/dashboard');
    } catch {
      return mockStore.getDashboardReport();
    }
  }

  async getAnalytics(days: number = 7): Promise<{
    daily_stats: {
      date: string;
      day: string;
      cafe_revenue: number;
      gaming_revenue: number;
      total_revenue: number;
      orders_count: number;
      sessions_count: number;
    }[];
    category_breakdown: any[];
  }> {
    try {
      return await this.request(`/reports/analytics?days=${days}`);
    } catch {
      return mockStore.getAnalytics(days);
    }
  }
}

export const api = new ApiService();
