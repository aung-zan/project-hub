import { register } from "app/api/Auth";
import type { UserCreate } from "app/types";
import { useState } from "react";

const useRegister = () => {
  const [error, setError] = useState<Record<string, string[]>>({});
  // can add loading state.

  const userRegister = async (request: UserCreate) => {
    const data = await register(request);

    if (
      data.success === false &&
      data.message &&
      typeof data.message == "object"
    ) {
      setError(data.message);
    }

    return data;
  };

  return { userRegister, error };
};

export default useRegister;
