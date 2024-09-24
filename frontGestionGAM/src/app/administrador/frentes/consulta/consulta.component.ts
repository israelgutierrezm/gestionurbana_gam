import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

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
    private modalService: NgbModal
  ) {
    this.frenteForm = this.formBuilder.group({
      cat_direccion_territorial_id: ['', Validators.required],
      cat_colonia_id: ['', Validators.required],
      cat_tipo_espacio_publico_id: ['', Validators.required],
      nombre: ['', Validators.required],
      area: ['', Validators.required],
      dias_jornada: ['', Validators.required],
      personal_necesario: ['', Validators.required]
    });
  }

  ngOnInit(): void {}

  openModal(content: any) {
    this.frenteForm.reset();
    this.modalService.open(content);
  }

  closeModal() {
    this.modalService.dismissAll();
  }

  enviarInformacion() {
  }
}


