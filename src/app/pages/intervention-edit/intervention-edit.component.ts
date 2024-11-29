import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { InterventionService } from '../../services/intervention.service';
import { Intervention, STATUS_TYPES } from '../../models/intervention.model';

@Component({
  selector: 'app-intervention-edit',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './intervention-edit.component.html',
  styleUrls: ['./intervention-edit.component.css']
})
export class InterventionEditComponent implements OnInit {
  editForm: FormGroup;
  intervention: Intervention | null = null;
  loading = false;
  error = '';
  readonly STATUS_TYPES = STATUS_TYPES;

  constructor(
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private interventionService: InterventionService
  ) {
    this.editForm = this.formBuilder.group({
      TypeIntervention: ['', Validators.required],
      Description: ['', Validators.required],
      DebutIntervention: ['', Validators.required],
      FinIntervention: ['', Validators.required],
      StatutIntervention: ['', Validators.required],
      Commentaires: ['']
    });
  }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadIntervention(parseInt(id, 10));
    }
  }

  loadIntervention(id: number) {
    this.loading = true;
    this.interventionService.getInterventionById(id).subscribe({
      next: (response) => {
        if (response.status === 'success' && response.data) {
          this.intervention = response.data;
          this.editForm.patchValue({
            TypeIntervention: this.intervention.TypeIntervention,
            Description: this.intervention.Description,
            DebutIntervention: this.intervention.DebutIntervention,
            FinIntervention: this.intervention.FinIntervention,
            StatutIntervention: this.intervention.StatutIntervention,
            Commentaires: this.intervention.Commentaires
          });
        } else {
          this.error = 'Erreur lors du chargement de l\'intervention';
        }
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Erreur lors du chargement de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }

  onSubmit() {
    if (this.editForm.invalid || !this.intervention?.InterventionID) {
      return;
    }

    this.loading = true;
    const updatedIntervention = {
      ...this.intervention,
      ...this.editForm.value
    };

    this.interventionService.updateIntervention(
      this.intervention.InterventionID,
      updatedIntervention
    ).subscribe({
      next: (response) => {
        if (response.status === 'success') {
          this.router.navigate(['/planning']);
        } else {
          this.error = 'Erreur lors de la mise à jour de l\'intervention';
        }
        this.loading = false;
      },
      error: (error) => {
        this.error = 'Erreur lors de la mise à jour de l\'intervention';
        this.loading = false;
        console.error('Erreur:', error);
      }
    });
  }

  getStatusOptions(): string[] {
    return Object.values(STATUS_TYPES);
  }
}