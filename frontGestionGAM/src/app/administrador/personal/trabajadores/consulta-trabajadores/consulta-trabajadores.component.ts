import { Component } from '@angular/core';
import { PersonalService } from '../../services/personal.service';
import { ToastService } from 'src/app/extras/toast/toast.service';

declare var alertify: any;

@Component({
  selector: 'app-consulta-trabajadores',
  templateUrl: './consulta-trabajadores.component.html',
  styleUrls: ['./consulta-trabajadores.component.scss']
})
export class ConsultaTrabajadoresComponent {
  arrayPersonas: Array<any> = [];

  constructor(
    private _personalService: PersonalService,
    private toast: ToastService
  ){
    this.consultaTrabajadores();
  }

  consultaTrabajadores(){
    this._personalService.consultaPersonasRol(2).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayPersonas = response['personas'];
        }
      }
    });
  }

  alertEliminaPersona(indicePersona: number, usuarioId: string) {
    alertify.confirm('', '¿Deseas eliminar este usuario?',
      () => this.eliminaPersona(indicePersona, usuarioId),
      () => this.cancelado()
      ).set('labels', { ok: 'Sí', cancel: 'No' });
  }

  eliminaPersona(indicePersona: number, usuarioId:string) {
    this._personalService.eliminaUsuario(usuarioId).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayPersonas.splice(indicePersona, 1);
          this.toast.show('Eliminado correctamente', { classname: 'bg-success' });
        }
      }
    });
  }

  cancelado() {
    this.toast.show('Cancelado', { classname: 'bg-danger' });
  }

}
