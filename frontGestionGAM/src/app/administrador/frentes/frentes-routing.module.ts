import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { FrentesComponent } from './frentes.component';

import { ConsultaComponent } from './consulta/consulta.component';
import { adminGuard } from 'src/app/guards/admin.guard';

const routes: Routes = [
  {
    path: '', component: FrentesComponent,
    children:[
      { path: '', redirectTo: 'consulta', pathMatch: 'full' },
      { path: 'consulta', component: ConsultaComponent,canActivate: [adminGuard] },

    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FrentesRoutingModule { }



