import { Component, TemplateRef } from '@angular/core';
import { ToastService } from './toast.service';
import { Toast } from './toast.model';

@Component({
  selector: 'app-toast',
  templateUrl: './toast.component.html',
  styleUrls: ['./toast.component.scss'],
  host: { 'class': 'toast-container position-fixed bottom-0 end-0 p-3', 'style': 'z-index: 1200' }
})
export class ToastComponent {

  constructor(public toastService: ToastService){}
  isTemplate(toast: Toast) { return toast.textOrTpl instanceof TemplateRef; }

}
