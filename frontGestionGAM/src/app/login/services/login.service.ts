import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { GLOBAL } from 'src/app/shared/globals/global';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  url: String = GLOBAL.url;

  constructor(private _httpClient: HttpClient){}

  _validaUserPass(form: FormGroup){
    let formData: FormData = new FormData();
    formData.append('usuario', form.get('user')?.value);
    formData.append('pass', form.get('pass')?.value);
    return this._httpClient.post(this.url + 'general/usuarios/validaUser.php', formData);
  }
}
