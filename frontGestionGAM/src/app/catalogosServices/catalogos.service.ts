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

}
