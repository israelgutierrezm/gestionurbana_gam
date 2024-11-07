import { Injectable } from '@angular/core';
import { GLOBAL } from '../shared/globals/global';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class PlataformaService {
  url: String = GLOBAL.url;

  constructor(
    private _httpClient: HttpClient
  ) { }

  getJWT() {
    let jwt = localStorage.getItem('jwt');
    if(!jwt){
      return null;
    }
    return jwt;
  }

  getJWTData(){
    const jwt = this.getJWT();
    return this._httpClient.get(this.url + 'general/checkToken.php?jwt=' + jwt);
  }

}
