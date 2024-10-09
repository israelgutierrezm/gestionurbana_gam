import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CatalogosService } from 'src/app/catalogosServices/catalogos.service';
import { ToastService } from 'src/app/extras/toast/toast.service';
import { PersonalService } from '../../services/personal.service';
import { UsuarioForm } from './form-personas.model';
import { GLOBAL } from 'src/app/shared/globals/global';

declare var alertify: any;

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})


export class FormComponent implements OnInit {
  title: string = 'Agregar nueva persona';
  personaForm: FormGroup;
  tiposSangre: Array<any> = [];
  arregloRoles: Array<any> = [];
  arregloGeneros: Array<any> = [];
  arregloComplexiones: Array<any> = [];
  arregloEdosCiviles: Array<any> = [];
  arregloSeguros: Array<any> = [];
  usuarioId: string | null;
  rolId: string | null;
  imagen: File | null = null;
  usuarioList: UsuarioForm | null = null;

  urlAssets = GLOBAL.urlAssets

  errorCurp: string = 'Ingresa una CURP válida'; 
  errorEmail: string = 'Ingresa un correo electrónico válido'; 


  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private _toast: ToastService,
    private _catalogoService: CatalogosService,
    private _personalService: PersonalService
  ) {
    this.usuarioId = this.route.snapshot.paramMap.get('usuarioId');
    this.rolId = this.route.snapshot.paramMap.get('rolId');

    this.personaForm = this.formBuilder.group({
      rol: ['', Validators.required],
      nombre: ['', Validators.required],
      apellidoPaterno: ['', Validators.required],
      apellidoMaterno: ['', Validators.required],
      curp: ['', [Validators.required, Validators.pattern(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)]],
      sexo: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      oficio: ['', Validators.required],
      edoCivil: ['', Validators.required],
      numeroTelefono: ['', Validators.required],
      numeroCelular: ['', Validators.required],
      email: ['', Validators.required],
      pass: ['password'],
      nombreContacto: ['', Validators.required],
      apellidoContacto: ['', Validators.required],
      parentescoContacto: ['', Validators.required],
      numeroContacto: ['', Validators.required],
      enfermedades: [''],
      alergias: [''],
      medicamentos: [''],
      estatura: ['', Validators.required],
      complexion: ['',Validators.required],
      tipoSangre: ['', Validators.required],
      sSocial: ['0', Validators.required],
      tipoSeguro: [''],
      numeroSeguro: ['']
    });
  }

  get personaFormControls() { return this.personaForm.controls; }

  ngOnInit() {
    const url = this.router.url;

    if (url.includes('/editar')) {
      this.title = 'Editar usuario';
    } else if (url.includes('/crea')) {
      this.title = 'Agregar nuevo usuario';
    }

    if (this.usuarioId) {
      this.consultaUsuarioForm();
    }

    if (this.rolId) {
      this.personaForm.get('rol')?.setValue(this.rolId);
      this.personaForm.get('rol')?.disable();
    }

    this.consultaCatTipoSangre();
    this.consultaRoles();
    this.consultaGeneros();
    this.consultaCatComplexion();
    this.consultaCatEdosCiviles();
    this.consultaCatTipoSeguros();

    this.personaForm.get('sSocial')?.valueChanges.subscribe(sSocialValor => {
      this.changeValidators(sSocialValor);
    });
  }

  getSexo() {
    if (this.personaFormControls['curp'].status == 'VALID') {
      const curp = this.personaFormControls['curp'].value
      const sexo = curp[10];
      if (sexo == 'H') {
        this.personaForm.get('sexo')?.setValue(1);
      }

      if (sexo == 'M') {
        this.personaForm.get('sexo')?.setValue(2);
      }
    }
  }

  validaCurp() {
    const curp = this.personaFormControls['curp'].value;
    this._personalService.validaCurp(curp).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.personaForm.get('curp')?.setErrors({invalid:true});
          this.errorCurp = response['msg'];
        }else{
          this.errorCurp = 'Ingresa una CURP válida';
          this.getSexo();
        }
      }
    });
  }

  validaEmail() {
    const email = this.personaFormControls['email'].value;
    this._personalService.validaEmail(email).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.errorEmail = response['msg'];
          this.personaForm.get('email')?.setErrors({invalid:true});
        }else{
          this.errorEmail = 'Ingresa un correo electrónico válido';
        }
      }
    });
  }

  changeValidators(sSocialValor: string){
    if(sSocialValor === '1'){
      this.personaForm.get('tipoSeguro')?.setValidators(Validators["required"]);
      this.personaForm.get('tipoSeguro')?.updateValueAndValidity();
    }else{
      this.personaForm.get('tipoSeguro')?.setValue('');
      this.personaForm.get('numeroSeguro')?.setValue('');

      this.personaForm.get('tipoSeguro')?.clearValidators();
      this.personaForm.get('tipoSeguro')?.updateValueAndValidity();
    }
  }

  consultaCatTipoSangre() {
    this._catalogoService.getCatalogoTipoSangre().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.tiposSangre = response['catalogo'];
        }
      }
    });
  }

  consultaCatTipoSeguros() {
    this._catalogoService.getTiposSeguros().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arregloSeguros = response['catalogo'];
        }
      }
    });
  }

  consultaCatComplexion() {
    this._catalogoService.getCatalogoComplexiones().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arregloComplexiones = response['catalogo'];
        }
      }
    });
  }

  consultaCatEdosCiviles() {
    this._catalogoService.getCatalogoEdosCiviles().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arregloEdosCiviles = response['catalogo'];
        }
      }
    });
  }

  consultaRoles() {
    this._catalogoService.getCatalogoRoles().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arregloRoles = response['catalogo'];
        }
      }
    });
  }

  consultaGeneros() {
    this._catalogoService.getCatalogoGeneros().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arregloGeneros = response['catalogo'];
        }
      }
    });
  }

  consultaUsuarioForm() {
    this._personalService.consultaEspPersona(this.usuarioId).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.usuarioList = response['usuario'];
          this.fillForm();
        }
      }
    });
  }

  fillForm() {
    this.personaForm.get('rol')?.setValue(this.usuarioList?.cat_rol_id);
    this.personaForm.get('nombre')?.setValue(this.usuarioList?.nombre);
    this.personaForm.get('apellidoPaterno')?.setValue(this.usuarioList?.ap_pat);
    this.personaForm.get('apellidoMaterno')?.setValue(this.usuarioList?.ap_mat);
    this.personaForm.get('curp')?.setValue(this.usuarioList?.curp);
    this.personaForm.get('sexo')?.setValue(this.usuarioList?.cat_genero_id);
    this.personaForm.get('fechaNacimiento')?.setValue(this.usuarioList?.fecha_nacimiento);
    this.personaForm.get('oficio')?.setValue(this.usuarioList?.oficio);
    this.personaForm.get('edoCivil')?.setValue(this.usuarioList?.estado_civil_id);
    this.personaForm.get('numeroTelefono')?.setValue(this.usuarioList?.telefono);
    this.personaForm.get('numeroCelular')?.setValue(this.usuarioList?.celular);
    this.personaForm.get('email')?.setValue(this.usuarioList?.email);
    this.personaForm.get('nombreContacto')?.setValue(this.usuarioList?.nombre_contacto);
    this.personaForm.get('apellidoContacto')?.setValue(this.usuarioList?.apellido_contacto);
    this.personaForm.get('parentescoContacto')?.setValue(this.usuarioList?.parentesco);
    this.personaForm.get('numeroContacto')?.setValue(this.usuarioList?.telefono_contacto);
    this.personaForm.get('enfermedades')?.setValue(this.usuarioList?.condiciones_preexistentes ?? '');
    this.personaForm.get('alergias')?.setValue(this.usuarioList?.alergias ?? '');
    this.personaForm.get('medicamentos')?.setValue(this.usuarioList?.medicamentos ?? '');
    this.personaForm.get('estatura')?.setValue(this.usuarioList?.estatura);
    this.personaForm.get('complexion')?.setValue(this.usuarioList?.complexion_id);
    this.personaForm.get('tipoSangre')?.setValue(this.usuarioList?.tipo_sangre);
    this.personaForm.get('sSocial')?.setValue(this.usuarioList?.seguro_social);
    if (this.usuarioId) {
      this.personaForm.get('pass')?.clearValidators();
      this.personaForm.get('pass')?.updateValueAndValidity();
    }
    
    if(this.usuarioList?.seguro_social == 1){
      this.personaForm.get('tipoSeguro')?.setValue(this.usuarioList?.tipo_seguro_id);
      this.personaForm.get('numeroSeguro')?.setValue(this.usuarioList?.numero_seguro);
      this.personaForm.get('tipoSeguro')?.setValidators(Validators["required"]);
      this.personaForm.get('tipoSeguro')?.updateValueAndValidity();
    }
  }

  enviarInformacion() {
    if (this.personaForm.invalid) {
      Object.keys(this.personaForm.controls).forEach(controlKey => {
        this.personaForm.controls[controlKey].markAsTouched();
        if(this.personaForm.controls[controlKey].invalid){
          console.log(this.personaForm.controls[controlKey]);
        }
      });
      return;
    }
    this._personalService.guardaPersona(this.personaForm, this.usuarioId, this.imagen).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this._toast.show(response['msg'], { classname: 'bg-success' });
          this.router.navigate(['/admin/personal/consulta-trabajadores']);
        } else {
          this._toast.show(response['Error al guardar'], { classname: 'bg-danger' });
        }
      }
    });
  }

  onImagenFileChange(file: File | null) {
    this.imagen = file;
  }

  alertEliminaImagen() {
    alertify.confirm('', '¿Deseas eliminar esta imagen de usuario?',
      () => this.eliminaImagen(),
      () => this.cancelado()
    ).set('labels', { ok: 'Sí', cancel: 'No' });
  }

  cancelado() {
    this._toast.show('Cancelado', { classname: 'bg-danger' });
  }

  eliminaImagen() {
    this._personalService.eliminaImagenPerfil(this.usuarioId).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.imagen = null;
          this.usuarioList!.url_foto = null;
          this._toast.show(response['msg'], { classname: 'bg-success' });
        } else {
          this._toast.show(response['msg'], { classname: 'bg-danger' });
        }
      }
    });
  }

}

