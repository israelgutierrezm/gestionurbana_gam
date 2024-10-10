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

  consultaPersonasRol(rolId: string | number){
    let params = new HttpParams()
    .set('rolId', rolId || '');
    return this._httpClient.get(this.url + 'administrador/personas/consultaPersonasRol.php', { params: params });
  }

  consultaEspPersona(usuarioId: string | null){
    let params = new HttpParams()
    .set('usuarioId', usuarioId || '');
    return this._httpClient.get(this.url + 'administrador/personas/consultaEspPersona.php', { params: params });
  }
  
  eliminaImagenPerfil(usuarioId: string | null){
    let params = new HttpParams()
    .set('usuarioId', usuarioId || '');
    return this._httpClient.get(this.url + 'administrador/personas/eliminaImagenPerfil.php', { params: params });
  }

  guardaPersona(form:FormGroup, usuarioId: string | null, imagen: File | null){
    let formData: FormData = new FormData();
    formData.append('rolId', form.get('rol')?.value);
    formData.append('matricula', form.get('matricula')?.value);
    formData.append('nombre', form.get('nombre')?.value);
    formData.append('apellidoPaterno', form.get('apellidoPaterno')?.value);
    formData.append('apellidoMaterno', form.get('apellidoMaterno')?.value);
    formData.append('curp', form.get('curp')?.value);
    formData.append('generoId', form.get('sexo')?.value);
    formData.append('fechaNacimiento', form.get('fechaNacimiento')?.value);
    formData.append('oficio', form.get('oficio')?.value);
    formData.append('edoCivil', form.get('edoCivil')?.value);
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
    formData.append('estatura', form.get('estatura')?.value);
    formData.append('complexion', form.get('complexion')?.value);
    formData.append('tipoSangre', form.get('tipoSangre')?.value);
    formData.append('sSocial', form.get('sSocial')?.value);
    formData.append('tipoSeguro', form.get('tipoSeguro')?.value);
    formData.append('numeroSeguro', form.get('numeroSeguro')?.value);
    formData.append('direccion', form.get('direccion')?.value);
    formData.append('area', form.get('area')?.value);
    formData.append('funcion', form.get('funcion')?.value);
    if(form.get('pass')?.value != ''){
      formData.append('pass', form.get('pass')?.value);
    }
    
    if(imagen){
      formData.append('imagen', imagen);
    }

    if(usuarioId){
      formData.append('usuarioId', usuarioId);
      return this._httpClient.post(this.url + 'administrador/personas/editaPersona.php', formData);
    }else{
      return this._httpClient.post(this.url + 'administrador/personas/creaPersona.php', formData);
    }
  }

  eliminaUsuario(usuarioId:string){
    let formData: FormData = new FormData();
    formData.append('usuarioId', usuarioId);
    return this._httpClient.post(this.url + 'administrador/personas/eliminaPersona.php', formData);
  }

  validaCurp(curp: string){
    let params = new HttpParams()
    .set('curp', curp);
    return this._httpClient.get(this.url + 'general/usuarios/validaCurp.php', { params: params });
  }

  validaEmail(email: string){
    let params = new HttpParams()
    .set('email', email);
    return this._httpClient.get(this.url + 'general/usuarios/validaCorreo.php', { params: params });
  }

}
