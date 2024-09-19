import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InventarioRoutingModule } from './inventario-routing.module';
import { InventarioComponent } from './inventario.component';
import { ConsultaComponent } from './consulta/consulta.component';


@NgModule({
  declarations: [
    InventarioComponent,
    ConsultaComponent,
  ],
  imports: [
    CommonModule,
    InventarioRoutingModule
  ]
})
export class InventarioModule { }
