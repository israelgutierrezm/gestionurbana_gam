import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastService } from '../extras/toast/toast.service';
import { LoginService } from './services/login.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private _toast: ToastService,
    private _loginService: LoginService
  ) {
    this.loginForm = this.formBuilder.group({
      correo: ['', Validators.required],
      pass: ['', Validators.required],
    });
  }

  get loginFormControls() { return this.loginForm.controls; }

  ngOnInit() {

  }

  enviarInformacion(){
    if (this.loginForm.invalid) {
      Object.keys(this.loginForm.controls).forEach(controlKey => {
        this.loginForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this._toast.show('Inicio de sesión exitoso',{ classname: 'bg-success' });
    this.router.navigate(['admin']);
  }
}
