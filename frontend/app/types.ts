export interface Response {
  success: boolean;
  error?: string;
  message?: string | Record<string, string[]>;
  data?: object;
}

export interface UserCreate {
  name: string;
  username: string;
  email: string;
  password: string;
  confirm_password: string;
}

export interface ConnectionConfig {
  path: string;
  method: "post" | "put" | "get";
  data?: UserCreate;
  token: boolean;
}
