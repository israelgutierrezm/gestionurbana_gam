import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PersonalRoutingModule } from './personal-routing.module';
import { PersonalComponent } from './personal.component';
import { ConsultaComponent } from './usuarios/consulta/consulta.component';
import { FormComponent } from './usuarios/form/form.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InfoPersonaComponent } from './usuarios/info-persona/info-persona.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { ConsultaSupervisoresComponent } from './supervisores/consulta-supervisores/consulta-supervisores.component';
import { ConsultaTrabajadoresComponent } from './trabajadores/consulta-trabajadores/consulta-trabajadores.component';
import { CredencialTrabajadoresComponent } from './trabajadores/credencial-trabajadores/credencial-trabajadores.component';


@NgModule({
  declarations: [
    PersonalComponent,
    ConsultaComponent,
    FormComponent,
    InfoPersonaComponent,
    DashboardComponent,
    ConsultaSupervisoresComponent,
    ConsultaTrabajadoresComponent,
    CredencialTrabajadoresComponent
  ],
  imports: [
    CommonModule,
    PersonalRoutingModule,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class PersonalModule { }
