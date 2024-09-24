import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { FrentesComponent } from './frentes.component';
import { ConsultaComponent } from './consulta/consulta.component';


const routes: Routes = [
  {
    path: '', component: FrentesComponent,
    children:[
      { path: '', redirectTo: 'consulta', pathMatch: 'full' },
      { path: 'consulta', component: ConsultaComponent },

    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FrentesRoutingModule { }

export class ActividadesRoutingModule { }
