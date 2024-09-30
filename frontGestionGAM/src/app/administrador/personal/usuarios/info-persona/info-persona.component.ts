import { Component } from '@angular/core';
import { PersonalService } from '../../services/personal.service';
import { UsuarioForm } from '../form/form-personas.model';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-info-persona',
  templateUrl: './info-persona.component.html',
  styleUrls: ['./info-persona.component.scss']
})
export class InfoPersonaComponent {
  usuarioList: UsuarioForm | null = null;
  usuarioId: string | null;



  constructor(
    private _personalService: PersonalService,
    private route: ActivatedRoute,
  ){
    this.usuarioId = this.route.snapshot.paramMap.get('usuarioId');
    this.consultaUsuario();
  }

  consultaUsuario(){
    this._personalService.consultaEspPersona(this.usuarioId).subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.usuarioList = response['usuario'];
        }
      }
    });
  }
}
