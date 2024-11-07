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
import { adminGuard } from 'src/app/guards/admin.guard';

const routes: Routes = [
  {
    path: '', component: PersonalComponent,
    children:[
      { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
      { path: 'dashboard', component: DashboardComponent , canActivate: [adminGuard]},
      { path: 'consulta-usuarios', component: ConsultaComponent , canActivate: [adminGuard]},
      { path: 'consulta-supervisores', component: ConsultaSupervisoresComponent , canActivate: [adminGuard]},
      { path: 'consulta-trabajadores', component: ConsultaTrabajadoresComponent , canActivate: [adminGuard]},
      { path: 'credencial/:usuarioId', component: CredencialTrabajadoresComponent , canActivate: [adminGuard]},
      { path: 'crea', component: FormComponent , canActivate: [adminGuard]},
      { path: 'crea/:rolId', component: FormComponent , canActivate: [adminGuard]},
      { path: 'editar/:usuarioId', component: FormComponent, canActivate: [adminGuard]},
      { path: 'info/:usuarioId', component: InfoPersonaComponent, canActivate: [adminGuard]},
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PersonalRoutingModule { }
