import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';  
import { FrentesRoutingModule } from './frentes-routing.module';
import { ConsultaComponent } from './consulta/consulta.component';
import { FormComponent } from './form/form.component';


@NgModule({
  declarations: [
    ConsultaComponent,
    FormComponent
  ],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    FrentesRoutingModule
  ],
  exports: [
    FormComponent  
  ]
})
export class FrentesModule { }
