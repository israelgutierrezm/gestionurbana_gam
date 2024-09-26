import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { GLOBAL } from 'src/app/shared/globals/global';

@Injectable({
  providedIn: 'root'
})
export class FrentesService {
  url: String = GLOBAL.url;

  constructor(private _httpClient: HttpClient) { }

  consultaFrentes(){
    return this._httpClient.get(this.url + 'administrador/frentes/consultaFrentes.php');
  }

  guardaFrente(form:FormGroup, frenteId: string | null){

    let formData: FormData = new FormData();
    formData.append('cat_direccion_territorial_id', form.get('cat_direccion_territorial_id')?.value);
    formData.append('area', form.get('area')?.value);
    formData.append('cat_colonia_id', form.get('cat_colonia_id')?.value);
    formData.append('nombre', form.get('nombre')?.value);
    formData.append('dias_jornada', form.get('dias_jornada')?.value);
    formData.append('personal_necesario', form.get('personal_necesario')?.value);
    let tiposEspaciosFrentes = form.get('cat_tipo_espacio_frente_id')?.value;
    if (Array.isArray(tiposEspaciosFrentes)) {
      formData.append('tiposEspaciosFrentes', tiposEspaciosFrentes.join(',')); 
    } else {
      formData.append('tiposEspaciosFrentes', tiposEspaciosFrentes);
    }   
    
    if(frenteId){
      formData.append('frenteId', frenteId);
      return this._httpClient.post(this.url + 'administrador/frentes/editaFrente.php', formData);
    }else{
      return this._httpClient.post(this.url + 'administrador/frentes/creaFrente.php', formData);
    }

  }

  eliminaFrente(frenteId:string){
    let formData: FormData = new FormData();
    formData.append('frenteId', frenteId);
    return this._httpClient.post(this.url + 'administrador/frentes/eliminaFrente.php', formData);
  }


}
