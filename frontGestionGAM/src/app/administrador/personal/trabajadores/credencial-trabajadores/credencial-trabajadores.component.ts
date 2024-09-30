import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { GLOBAL } from 'src/app/shared/globals/global';

@Component({
  selector: 'app-credencial-trabajadores',
  templateUrl: './credencial-trabajadores.component.html',
  styleUrls: ['./credencial-trabajadores.component.scss']
})
export class CredencialTrabajadoresComponent implements OnInit {
  urlApi = GLOBAL.url;
  usuarioId: string | null;

  constructor(private route: ActivatedRoute){
    this.usuarioId = this.route.snapshot.paramMap.get('usuarioId');
  }

  ngOnInit(){

  }

  descargaCredencial(){
    var params = '?usuarioId=' + this.usuarioId;
    window.open(this.urlApi+'reportes/credencial/credencial.php'+params)
  }

}
