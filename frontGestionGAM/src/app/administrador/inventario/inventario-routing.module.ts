import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { InventarioComponent } from './inventario.component';
import { ConsultaComponent } from './consulta/consulta.component';

const routes: Routes = [
  {
    path: '', component: InventarioComponent,
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
export class InventarioRoutingModule { }
