import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})

export class FormComponent implements OnInit {
  personaForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router
  ){
    this.personaForm = this.formBuilder.group({
      nombre: ['', Validators.required],
      apellidoPaterno: ['', Validators.required],
      apellidoMaterno: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      curp: ['', [Validators.required,Validators.pattern(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)]],
      sexo: ['', Validators.required],
      numeroTelefono: ['', Validators.required],
      enfermedades: [''],
      alergias: [''],
      medicamentos: [''],
      tipoSangre: ['', Validators.required],
      nombreContacto: ['', Validators.required],
      parentescoContacto: ['', Validators.required],
      numeroContacto: ['', Validators.required],
      rol: ['', Validators.required]
    });
  }

  get personaFormControls() { return this.personaForm.controls;}

  ngOnInit(){

  }

  enviarInformacion(){
    if (this.personaForm.invalid) {
      Object.keys(this.personaForm.controls).forEach(controlKey => {
        this.personaForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this.router.navigate(['consulta']);
  }

  getSexo(){
    if(this.personaFormControls['curp'].status == 'VALID'){
      const curp = this.personaFormControls['curp'].value
      const sexo = curp[10];
      this.personaForm.get('sexo')?.setValue(sexo);
    }
  }

}

