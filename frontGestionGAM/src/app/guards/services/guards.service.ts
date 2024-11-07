import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { PlataformaService } from 'src/app/services/plataforma.service';
import { GLOBAL } from 'src/app/shared/globals/global';

@Injectable({
  providedIn: 'root'
})
export class GuardsService {
  url: String = GLOBAL.url;

  constructor(private plataformaService: PlataformaService,
    private _httpClient: HttpClient
  ) { }

  verificaJWTGuard(rolId: number): Promise<boolean> {
    return new Promise((resolve, reject) => {
      const jwt = this.plataformaService.getJWT();
      if (!jwt) {
        resolve(false);
        return;
      }
      this.validaJWT(jwt).subscribe({
        next: (response: any) => {
          if (response && response['estatus'] == 1 && response['usuario'].rol_id == rolId) {
            resolve(true);
          } else {
            resolve(false);
          }
        },
      });
    });
  }
  
  validaJWT(jwt: string) {
    return this._httpClient.get(this.url + 'general/checkToken.php?jwt=' + jwt);
  }
}
