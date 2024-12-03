import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PreposeComponent } from './prepose.component';

describe('PreposeComponent', () => {
  let component: PreposeComponent;
  let fixture: ComponentFixture<PreposeComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PreposeComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PreposeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
