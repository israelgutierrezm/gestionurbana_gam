import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { AdministradorComponent } from './administrador.component';
import { adminGuard } from '../guards/admin.guard';

const routes: Routes = [
  {
      path: '', component: AdministradorComponent,
      children: [
        { path: '', redirectTo: 'home', pathMatch: 'full' },
        { path: 'home', component: HomeComponent,canActivate: [adminGuard]},
        { path: 'personal', loadChildren: () => import('./personal/personal.module').then(m => m.PersonalModule),canActivate: [adminGuard]},
        { path: 'actividades', loadChildren: () => import('./actividades/actividades.module').then(m => m.ActividadesModule),canActivate: [adminGuard]},
        { path: 'frentes', loadChildren: () => import('./frentes/frentes.module').then(m => m.FrentesModule),canActivate: [adminGuard]},
        { path: 'inventario', loadChildren: () => import('./inventario/inventario.module').then(m => m.InventarioModule),canActivate: [adminGuard]},
        { path: '**', redirectTo: 'home', pathMatch: 'full' },
      ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdministradorRoutingModule { }



