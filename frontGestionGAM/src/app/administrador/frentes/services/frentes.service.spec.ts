import { TestBed } from '@angular/core/testing';

import { FrentesService } from './frentes.service';

describe('FrentesService', () => {
  let service: FrentesService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(FrentesService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
