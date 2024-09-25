import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ConsultaSupervisoresComponent } from './consulta-supervisores.component';

describe('ConsultaSupervisoresComponent', () => {
  let component: ConsultaSupervisoresComponent;
  let fixture: ComponentFixture<ConsultaSupervisoresComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ConsultaSupervisoresComponent]
    });
    fixture = TestBed.createComponent(ConsultaSupervisoresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
