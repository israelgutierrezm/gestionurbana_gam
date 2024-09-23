import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ConsultaComponent } from './personas/consulta/consulta.component';
import { PersonalComponent } from './personal.component';
import { FormComponent } from './personas/form/form.component';
import { InfoPersonaComponent } from './personas/info-persona/info-persona.component';

const routes: Routes = [
  {
    path: '', component: PersonalComponent,
    children:[
      { path: '', redirectTo: 'consulta', pathMatch: 'full' },
      { path: 'consulta', component: ConsultaComponent },
      { path: 'crea', component: FormComponent },
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
