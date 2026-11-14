// Native browser Intl formatter (zero dependencies needed)
export const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date);
};

// Short date only formatter (e.g. "Nov 1, 2026")
export const formatShortDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(date);
};

// date formatter (e.g. "Nov 1, 2026 · 11:36 PM")
export const formatDateTime = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);

  // Optional: guard against invalid date strings
  if (Number.isNaN(date.getTime())) return '-';

  const datePart = new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  }).format(date);

  const timePart = new Intl.DateTimeFormat('en-US', {
    hour: 'numeric',
    minute: 'numeric',
  }).format(date);

  return `${datePart} · ${timePart}`;
};

// Capitalize and format enum strings (e.g. "in_progress" -> "In Progress")
export const formatEnum = (str) => {
  if (!str) return '';
  return str.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
};

export const getInitials = (name) => {
  if (!name) return 'U';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};
