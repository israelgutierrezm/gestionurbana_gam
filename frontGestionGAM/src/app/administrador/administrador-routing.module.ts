import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { AdministradorComponent } from './administrador.component';

const routes: Routes = [
  {
      path: '', component: AdministradorComponent,
      children: [
        { path: '', redirectTo: 'home', pathMatch: 'full' },
        { path: 'home', component: HomeComponent },
        { path: 'personal', loadChildren: () => import('./personal/personal.module').then(m => m.PersonalModule)},
        { path: 'actividades', loadChildren: () => import('./actividades/actividades.module').then(m => m.ActividadesModule)},
        { path: 'frentes', loadChildren: () => import('./frentes/frentes.module').then(m => m.FrentesModule)},
        { path: 'inventario', loadChildren: () => import('./inventario/inventario.module').then(m => m.InventarioModule)},
        { path: '**', redirectTo: 'home', pathMatch: 'full' },
      ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdministradorRoutingModule { }
