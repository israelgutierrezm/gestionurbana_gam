import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaTrabajadoresComponent } from './consulta-trabajadores.component';

describe('ConsultaTrabajadoresComponent', () => {
  let component: ConsultaTrabajadoresComponent;
  let fixture: ComponentFixture<ConsultaTrabajadoresComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ConsultaTrabajadoresComponent]
    });
    fixture = TestBed.createComponent(ConsultaTrabajadoresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
