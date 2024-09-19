import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ConsultaComponent } from './personas/consulta/consulta.component';
import { DashboardPersonalComponent } from './dashboard-personal/dashboard-personal.component';
import { PersonalComponent } from './personal.component';
import { FormComponent } from './personas/form/form.component';
import { InfoPersonaComponent } from './personas/info-persona/info-persona.component';

const routes: Routes = [
  {
    path: '', component: PersonalComponent,
    children:[
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { path: 'dashboard', component: DashboardPersonalComponent },
      { path: 'consulta', component: ConsultaComponent },
      { path: 'crea', component: FormComponent },
      { path: 'editar/:personaId', component: FormComponent},
      { path: 'info/:personaId', component: InfoPersonaComponent},
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PersonalRoutingModule { }
