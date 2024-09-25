import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ConsultaComponent } from './usuarios/consulta/consulta.component';
import { PersonalComponent } from './personal.component';
import { FormComponent } from './usuarios/form/form.component';
import { InfoPersonaComponent } from './usuarios/info-persona/info-persona.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { ConsultaSupervisoresComponent } from './supervisores/consulta-supervisores/consulta-supervisores.component';
import { ConsultaTrabajadoresComponent } from './trabajadores/consulta-trabajadores/consulta-trabajadores.component';
import { CredencialTrabajadoresComponent } from './trabajadores/credencial-trabajadores/credencial-trabajadores.component';

const routes: Routes = [
  {
    path: '', component: PersonalComponent,
    children:[
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { path: 'dashboard', component: DashboardComponent },
      { path: 'consulta-usuarios', component: ConsultaComponent },
      { path: 'consulta-supervisores', component: ConsultaSupervisoresComponent },
      { path: 'consulta-trabajadores', component: ConsultaTrabajadoresComponent },
      { path: 'credencial/:usuarioId', component: CredencialTrabajadoresComponent },
      { path: 'crea', component: FormComponent },
      { path: 'crea/:rolId', component: FormComponent },
      { path: 'editar/:usuarioId', component: FormComponent},
      { path: 'info/:usuarioId', component: InfoPersonaComponent},
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PersonalRoutingModule { }
