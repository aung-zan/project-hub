export interface AppErrorT extends Error {
  statusCode: number;
  messageDetail: string | object;
}

export interface ResponseT {
  success: boolean;
  error?: string;
  message?: string | object;
  data?: object;
}

export interface UserCreate {
  name: string;
  username: string;
  email: string;
  password: string;
  confirm_password: string;
}

export interface UserLogin {
  email: string;
  password: string;
}

export interface UserUpdate {
  name?: string;
  password?: string;
  confirm_password?: string;
}

export interface ProjectCreate {
  name: string;
  description?: string;
  status: string;
  start_date?: string;
  end_date?: string;
}

export interface ProjectParams {
  id: string;
}

export interface ProjectUpdate {
  name?: string;
  description?: string;
  status?: string;
  start_date?: string;
  end_date?: string;
}

export interface ProjectMemberInfo {
  id: string;
  role: string;
}

export interface ProjectMembers {
  members: Array<ProjectMemberInfo>;
}

export interface ProjectMemberParams {
  id: string;
  memberId: string;
}

export interface TaskCreate {
  title: string;
  description?: string;
  status?: string;
  priority?: string;
  assigned_to?: string;
  due_date?: string;
}

export interface TaskParams {
  id: string;
  taskId: string;
}

export interface TaskUpdate {
  title?: string;
  description?: string;
  status?: string;
  priority?: string;
  assigned_to?: string;
  due_date?: string;
}

export interface CommentStoreParams {
  id: string;
}

// TODO: update this code.
export interface CommentStore {}

export interface CommentUpdateParams extends CommentStoreParams {
  commentId: string;
}

// TODO: update this code.
export interface CommentUpdate {}
