import { Component, OnInit } from '@angular/core';
import { PersonalService } from '../../services/personal.service';

@Component({
  selector: 'app-consulta',
  templateUrl: './consulta.component.html',
  styleUrls: ['./consulta.component.scss']
})
export class ConsultaComponent implements OnInit {
  arrayPersonas: Array<any> = []; 
  constructor(
    private _personalService: PersonalService
  ){

  }
  
  ngOnInit(){
    this.consultaPersonas();
  }

  consultaPersonas(){
    this._personalService.consultaPersonas().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.arrayPersonas = response['personas'];
          console.log(this.arrayPersonas);
          
        }
      }
    });
  }
}
