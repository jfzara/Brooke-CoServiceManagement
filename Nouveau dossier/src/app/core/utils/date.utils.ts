// src/app/core/utils/date.utils.ts
import { DisponibiliteHebdomadaire } from '../../models/technicien.model';

export interface TimeSlot {
  start: Date;
  end: Date;
}

export function parseTimeSlots(disponibilites: DisponibiliteHebdomadaire[]): TimeSlot[] {
  return disponibilites.map(dispo => ({
    start: new Date(dispo.DebutDisponibilite),
    end: new Date(dispo.FinDisponibilite)
  }));
}

export function formatDateToISO(date: Date): string {
  return date.toISOString().split('T')[0];
}

export function formatTimeToHHMM(date: Date): string {
  return date.toTimeString().slice(0, 5);
}

export function isTimeSlotAvailable(
  timeSlot: TimeSlot,
  existingSlots: TimeSlot[]
): boolean {
  return !existingSlots.some(existing =>
    (timeSlot.start < existing.end && timeSlot.end > existing.start)
  );
}

export function getWeekNumber(date: Date): number {
  const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
  const pastDaysOfYear = (date.getTime() - firstDayOfYear.getTime()) / 86400000;
  return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
}

export function addDays(date: Date, days: number): Date {
  const result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

export function getWorkingHours(date: Date): TimeSlot[] {
  const slots: TimeSlot[] = [];
  const startHour = 8;
  const endHour = 18;

  for (let hour = startHour; hour < endHour; hour++) {
    slots.push({
      start: new Date(date.setHours(hour, 0, 0, 0)),
      end: new Date(date.setHours(hour + 1, 0, 0, 0))
    });
  }

  return slots;
}

export function isWorkingDay(date: Date): boolean {
  const day = date.getDay();
  return day !== 0 && day !== 6; // 0 = Dimanche, 6 = Samedi
}

export function getDurationInHours(start: Date, end: Date): number {
  return (end.getTime() - start.getTime()) / (1000 * 60 * 60);
}

export function isValidTimeRange(start: Date, end: Date): boolean {
  return start < end && 
         start.getHours() >= 8 && 
         end.getHours() <= 18 && 
         isWorkingDay(start) && 
         isWorkingDay(end);
}