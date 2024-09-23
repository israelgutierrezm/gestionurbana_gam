import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { GLOBAL } from 'src/app/shared/globals/global';

@Injectable({
  providedIn: 'root'
})
export class PersonalService {
  url: String = GLOBAL.url;

  constructor(private _httpClient: HttpClient) { }

  consultaPersonas(){
    return this._httpClient.get(this.url + 'administrador/personas/consultaPersonas.php');
  }

  consultaEspPersona(usuarioId: string | null){
    let params = new HttpParams()
    .set('usuarioId', usuarioId || '');
    return this._httpClient.get(this.url + 'administrador/personas/consultaEspPersona.php', { params: params });
  }

  guardaPersona(form:FormGroup, usuarioId: string | null){
    let formData: FormData = new FormData();
    formData.append('rolId', form.get('rol')?.value);
    formData.append('nombre', form.get('nombre')?.value);
    formData.append('apellidoPaterno', form.get('apellidoPaterno')?.value);
    formData.append('apellidoMaterno', form.get('apellidoMaterno')?.value);
    formData.append('curp', form.get('curp')?.value);
    formData.append('generoId', form.get('sexo')?.value);
    formData.append('fechaNacimiento', form.get('fechaNacimiento')?.value);
    formData.append('numeroTelefono', form.get('numeroTelefono')?.value);
    formData.append('numeroCelular', form.get('numeroCelular')?.value);
    formData.append('email', form.get('email')?.value);
    formData.append('nombreContacto', form.get('nombreContacto')?.value);
    formData.append('apellidoContacto', form.get('apellidoContacto')?.value);
    formData.append('parentescoContacto', form.get('parentescoContacto')?.value);
    formData.append('numeroContacto', form.get('numeroContacto')?.value);
    formData.append('enfermedades', form.get('enfermedades')?.value);
    formData.append('alergias', form.get('alergias')?.value);
    formData.append('medicamentos', form.get('medicamentos')?.value);
    formData.append('tipoSangre', form.get('tipoSangre')?.value);
    if(usuarioId){
      formData.append('usuarioId', usuarioId);
      return this._httpClient.post(this.url + 'administrador/personas/editaPersona.php', formData);
    }
    return this._httpClient.post(this.url + 'administrador/personas/creaPersona.php', formData);
  }

  eliminaUsuario(usuarioId:string){
    let formData: FormData = new FormData();
    formData.append('usuarioId', usuarioId);
    return this._httpClient.post(this.url + 'administrador/personas/eliminaPersona.php', formData);
  }

}