import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PersonalRoutingModule } from './personal-routing.module';
import { PersonalComponent } from './personal.component';
import { ConsultaComponent } from './personas/consulta/consulta.component';
import { FormComponent } from './personas/form/form.component';
import { DashboardPersonalComponent } from './dashboard-personal/dashboard-personal.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InfoPersonaComponent } from './personas/info-persona/info-persona.component';


@NgModule({
  declarations: [
    PersonalComponent,
    ConsultaComponent,
    FormComponent,
    DashboardPersonalComponent,
    InfoPersonaComponent
  ],
  imports: [
    CommonModule,
    PersonalRoutingModule,
    FormsModule,
    ReactiveFormsModule
  ]
})
export class PersonalModule { }
