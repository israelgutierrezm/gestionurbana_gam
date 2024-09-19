import { Injectable, TemplateRef } from '@angular/core';
import { Toast } from './toast.model';

@Injectable({
  providedIn: 'root'
})
export class ToastService {

  constructor() { }

  toasts: any[] = [];

  show(textOrTpl: string | TemplateRef<any>, options: Partial<Toast> = {}) {
    this.toasts.push({ textOrTpl, ...options });
  }

  remove(toast: Toast) {
    this.toasts = this.toasts.filter(t => t !== toast);
  }

  clear() {
    this.toasts = [];
  }
}
