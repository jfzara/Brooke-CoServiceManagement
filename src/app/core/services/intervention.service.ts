// src/app/core/services/intervention.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { Intervention } from '../../models/intervention.model';
import { ApiResponse } from '../../models/api.model';

@Injectable({
  providedIn: 'root'
})
export class InterventionService {
  private apiUrl = 'http://localhost:8000/api.php';

  constructor(private http: HttpClient) { }

  getAllInterventions(): Observable<ApiResponse<Intervention[]>> {
    return this.http.get<ApiResponse<Intervention[]>>(
      `${this.apiUrl}?action=get_all_interventions`
    ).pipe(
      tap((response: ApiResponse<Intervention[]>) => console.log('Réponse API getAllInterventions:', response)),
      catchError((error: Error) => {
        console.error('Erreur API getAllInterventions:', error);
        throw error;
      })
    );
  }

  createIntervention(intervention: Partial<Intervention>): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=create_intervention`,
      intervention
    ).pipe(
      tap((response: ApiResponse<void>) => console.log('Réponse création intervention:', response)),
      catchError((error: Error) => {
        console.error('Erreur création intervention:', error);
        throw error;
      })
    );
  }

  getTechnicienInterventions(technicienId: number): Observable<ApiResponse<Intervention[]>> {
    return this.http.get<ApiResponse<Intervention[]>>(
      `${this.apiUrl}?action=get_technicien_interventions&technicienId=${technicienId}`
    ).pipe(
      tap((response: ApiResponse<Intervention[]>) => console.log('Réponse API:', response)),
      catchError((error: Error) => {
        console.error('Erreur API:', error);
        throw error;
      })
    );
  }

  getPlanningByTechnicienWithMoreInfos(technicienId: number): Observable<ApiResponse<Intervention[]>> {
    return this.getTechnicienInterventions(technicienId);
  }

  updateStatusIntervention(interventionId: number, status: string): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_intervention_status`,
      {
        InterventionID: interventionId,
        StatutIntervention: status
      }
    ).pipe(
      tap((response: ApiResponse<void>) => console.log('Réponse de mise à jour du statut:', response)),
      catchError((error: Error) => {
        console.error('Erreur de mise à jour du statut:', error);
        throw error;
      })
    );
  }

  getInterventionById(id: number): Observable<ApiResponse<Intervention>> {
    return this.http.get<ApiResponse<Intervention>>(
      `${this.apiUrl}?action=get_intervention&id=${id}`
    ).pipe(
      tap((response: ApiResponse<Intervention>) => console.log('Détails de l\'intervention:', response)),
      catchError((error: Error) => {
        console.error('Erreur lors de la récupération de l\'intervention:', error);
        throw error;
      })
    );
  }

  updateIntervention(id: number, data: Partial<Intervention>): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=update_intervention`,
      { id, ...data }
    ).pipe(
      tap((response: ApiResponse<void>) => console.log('Réponse de mise à jour:', response)),
      catchError((error: Error) => {
        console.error('Erreur de mise à jour:', error);
        throw error;
      })
    );
  }

  deleteIntervention(id: number): Observable<ApiResponse<void>> {
    return this.http.delete<ApiResponse<void>>(
      `${this.apiUrl}?action=delete_intervention&id=${id}`
    ).pipe(
      tap((response: ApiResponse<void>) => console.log('Réponse de suppression:', response)),
      catchError((error: Error) => {
        console.error('Erreur de suppression:', error);
        throw error;
      })
    );
  }

  assignerTechnicien(interventionId: number, technicienId: number): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=assigner_technicien`,
      {
        interventionId,
        technicienId
      }
    ).pipe(
      tap((response: ApiResponse<void>) => console.log('Réponse assignation technicien:', response)),
      catchError((error: Error) => {
        console.error('Erreur lors de l\'assignation du technicien:', error);
        throw error;
      })
    );
  }

  notifyTechnicien(interventionId: number, technicienId: number): Observable<ApiResponse<void>> {
    return this.http.post<ApiResponse<void>>(
      `${this.apiUrl}?action=notify_technicien`,
      { interventionId, technicienId }
    ).pipe(
      tap(response => console.log('Notification envoyée:', response)),
      catchError(error => {
        console.error('Erreur notification:', error);
        throw error;
      })
    );
  }



}