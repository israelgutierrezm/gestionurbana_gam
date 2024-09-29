import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';  
import { FrentesRoutingModule } from './frentes-routing.module';
import { ConsultaComponent } from './consulta/consulta.component';
import { FormComponent } from './form/form.component';
import { FormsModule } from '@angular/forms'; 


@NgModule({
  declarations: [
    ConsultaComponent,
    FormComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    FrentesRoutingModule
  ],
  exports: [
    FormComponent  
  ]
})
export class FrentesModule { }
