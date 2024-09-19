import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AdministradorRoutingModule } from './administrador-routing.module';
import { AdministradorComponent } from './administrador.component';
import { HomeComponent } from './home/home.component';
import { BodyComponent } from './components/body/body.component';
import { SidebarComponent } from './components/sidebar/sidebar.component';


@NgModule({
  declarations: [
    AdministradorComponent,
    HomeComponent,
    BodyComponent,
    SidebarComponent
  ],
  imports: [
    CommonModule,
    AdministradorRoutingModule,
  ]
})
export class AdministradorModule { }
