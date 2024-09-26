import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { FrentesService } from '../services/frentes.service';
import { ToastService } from 'src/app/extras/toast/toast.service';
import { ActivatedRoute, Router } from '@angular/router';


@Component({
  selector: 'app-consulta',
  templateUrl: './consulta.component.html',
  styleUrls: ['./consulta.component.scss']
})
export class ConsultaComponent implements OnInit {
  frenteForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private http: HttpClient,
    private modalService: NgbModal,
    private router: Router,
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

  }

  openModal(content: any) {
    this.frenteForm.reset();
    this.modalService.open(content, { size: 'lg' });
  }

  closeModal() {
    this.modalService.dismissAll();
  }
  enviarInformacion() {
    console.log('Tipo de Espacio Público seleccionado:', this.frenteForm.get('cat_tipo_espacio_frente_id')?.value);

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
  
}


