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
export class LoginComponent {
  loginForm: FormGroup;
  showFooter: boolean = true;
  // jwt: string;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private _toast: ToastService,
    private _loginService: LoginService
  ) {
    this.loginForm = this.formBuilder.group({
      user: ['', Validators.required],
      pass: ['', Validators.required],
    });
  }

  get loginFormControls() { return this.loginForm.controls; }

  log(){
    if (this.loginForm.invalid) {
      Object.keys(this.loginForm.controls).forEach(controlKey => {
        this.loginForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this.validaUserPass();
  }
  
  validaUserPass(){
    this._loginService._validaUserPass(this.loginForm).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this._toast.show('Inicio de sesión exitoso',{ classname: 'bg-success' });
          this.router.navigate(['admin']);
          localStorage.setItem('jwt',JSON.stringify(response['jwt']));
          localStorage.setItem('user',JSON.stringify(response['usuario']));
        }else{
          this._toast.show(response['msg'],{ classname: 'bg-danger' });
        }
      }
    });
  }
}
