import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { InputImagesComponent } from './input-images.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';



@NgModule({
  declarations: [InputImagesComponent],
  imports: [
    CommonModule
  ],
  exports:[
    InputImagesComponent
  ]
})
export class InputImagesModule { }
