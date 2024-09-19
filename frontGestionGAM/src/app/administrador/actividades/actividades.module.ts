import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ActividadesRoutingModule } from './actividades-routing.module';
import { ActividadesComponent } from './actividades.component';
import { ConsultaComponent } from './consulta/consulta.component';


@NgModule({
  declarations: [
    ActividadesComponent,
    ConsultaComponent,
  ],
  imports: [
    CommonModule,
    ActividadesRoutingModule
  ]
})
export class ActividadesModule { }
