import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { GLOBAL } from '../shared/globals/global';

@Injectable({
  providedIn: 'root'
})
export class CatalogosService {
  url: String = GLOBAL.url;

  constructor(private _httpClient: HttpClient) { }

  getCatalogoTipoSangre(){
    return this._httpClient.get(this.url +'catalogos/catTipoSangre.php');
  }

  getCatalogoRoles(){
    return this._httpClient.get(this.url +'catalogos/catRoles.php');
  }

  getCatalogoGeneros(){
    return this._httpClient.get(this.url +'catalogos/catGeneros.php');
  }

  getDireccionesTerritoriales(){
    return this._httpClient.get(this.url +'catalogos/catDireccionesTerritoriales.php');
  }

  getTiposEspaciosPublicosDT(){
    return this._httpClient.get(this.url +'catalogos/catEspaciosPublicosFrentes.php');
  }

  getColoniasGAM(){
    return this._httpClient.get(this.url +'catalogos/catColoniasGAM.php');
  }

}
