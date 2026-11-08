// Validates presence of value
export const required = (fieldName = 'Field') => {
  return (val) => (val !== null && val !== undefined && val !== '') || `${fieldName} is required`;
};

// Validates email syntax
export const isValidEmail = (val) => {
  if (!val) return 'Email is required';
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(val) || 'Please enter a valid email address';
};

// Validates minimum character length
export const minLength = (min, fieldName = 'Field') => {
  return (val) => !val || val.length >= min || `${fieldName} must be at least ${min} characters`;
};
