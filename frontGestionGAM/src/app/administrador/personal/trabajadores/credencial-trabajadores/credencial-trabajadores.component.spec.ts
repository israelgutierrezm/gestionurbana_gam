import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CredencialTrabajadoresComponent } from './credencial-trabajadores.component';

describe('CredencialTrabajadoresComponent', () => {
  let component: CredencialTrabajadoresComponent;
  let fixture: ComponentFixture<CredencialTrabajadoresComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [CredencialTrabajadoresComponent]
    });
    fixture = TestBed.createComponent(CredencialTrabajadoresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
