import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { FrentesService } from '../services/frentes.service';
import { ToastService } from 'src/app/extras/toast/toast.service';
import { ActivatedRoute, Router } from '@angular/router';

declare var alertify: any;

@Component({
  selector: 'app-consulta',
  templateUrl: './consulta.component.html',
  styleUrls: ['./consulta.component.scss']
})
export class ConsultaComponent implements OnInit {
  frenteForm: FormGroup;
  arrayFrentes: any[] = []; 
  constructor(
    private formBuilder: FormBuilder,
    private modalService: NgbModal,
    private _toast: ToastService,
    private _frentesService: FrentesService

  ) {
    this.frenteForm = this.formBuilder.group({
      cat_direccion_territorial_id: ['', Validators.required],
      cat_colonia_id: ['', Validators.required],
      cat_tipo_espacio_frente_id: [[], Validators.required], 
      nombre: ['', Validators.required],
      area: ['', Validators.required],
      dias_jornada: ['', Validators.required],
      personal_necesario: ['', Validators.required],
      
    });
  }

  ngOnInit() {
    this.consultaFrentes();
  }

  consultaFrentes(){
    this._frentesService.consultaFrentes().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayFrentes = response['frentes'];
        }
      }
    });
  }

  openModal(content: any) {
    this.frenteForm.reset();
    this.modalService.open(content, { size: 'lg' });
  }

  closeModal() {
    this.modalService.dismissAll();
  }

  enviarInformacion() {
    if (this.frenteForm.valid) {
      this._frentesService.guardaFrente(this.frenteForm, null).subscribe({
        next: (response: any) => {
          if(response && response['estatus']){
            this._toast.show(response['msg'], { classname: 'bg-success' });
            this.frenteForm.reset();
            this.modalService.dismissAll();
          }else{
            this._toast.show(response['msg'], { classname: 'bg-danger' });
          }
        }
      });

    } else 
      this.frenteForm.markAllAsTouched();
    
  }

  alertEliminaFrente(indiceFrente: number, frenteId: string) {
    alertify.confirm('', '¿Deseas eliminar este frente?',
      () => this.eliminaFrente(indiceFrente, frenteId),
      () => this.cancelado()
      ).set('labels', { ok: 'Sí', cancel: 'No' });
  }

  eliminaFrente(indiceFrente: number, frenteId:string) {
    this._frentesService.eliminaFrente(frenteId).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayFrentes.splice(indiceFrente, 1);
          this._toast.show('Eliminado correctamente', { classname: 'bg-success' });
        }
      }
    });
  }

  cancelado() {
    this._toast.show('Cancelado', { classname: 'bg-danger' });
  }
}


