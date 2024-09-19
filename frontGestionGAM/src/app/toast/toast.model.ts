import { TemplateRef } from '@angular/core';

export interface Toast {
  textOrTpl: string | TemplateRef<any>;
  delay?: number;
  classname?: string;
}