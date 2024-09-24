import { Component, ViewChild } from '@angular/core';
import { NgbModal} from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-consulta',
  templateUrl: './consulta.component.html',
  styleUrls: ['./consulta.component.scss']
})
export class ConsultaComponent {

  constructor(
    private _modalService: NgbModal,
  ){
  }
  // @ViewChild('successTpl') successAlert: ;


  openModal(content:any){
    this._modalService.open(content, { backdrop: 'static', keyboard: false, size: 'xl' });
  }

  closeModal(){
    this._modalService.dismissAll();
  }

  guardaInformacion(){

  }
}