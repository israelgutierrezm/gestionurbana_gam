import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdministradorRoutingModule } from './administrador-routing.module';
import { AdministradorComponent } from './administrador.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { BodyComponent } from './body/body.component';
import { SidebarComponent } from './sidebar/sidebar.component';


@NgModule({
  declarations: [
    AdministradorComponent,
    DashboardComponent,
    BodyComponent,
    SidebarComponent
  ],
  imports: [
    CommonModule,
    AdministradorRoutingModule,
  ]
})
export class AdministradorModule { }
