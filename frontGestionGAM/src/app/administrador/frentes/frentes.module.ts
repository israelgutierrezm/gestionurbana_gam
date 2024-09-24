import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FrentesRoutingModule } from './frentes-routing.module';
import { FrentesComponent } from './frentes.component';
import { ConsultaComponent } from './consulta/consulta.component';


@NgModule({
  declarations: [
    FrentesComponent,
    ConsultaComponent
  ],
  imports: [
    CommonModule,
    FrentesRoutingModule
  ]
})
export class FrentesModule { }
