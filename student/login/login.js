// Student Login JS

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      loginUser();
    });
  }
});

function loginUser() {
  // Placeholder for backend API call
  fetch("/api/student/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      email: document.querySelector("[name=email]").value,
      password: document.querySelector("[name=password]").value,
    }),
  })
    .then((response) => {
      if (!response.ok) throw new Error("Login failed");
      return response.json();
    })
    .then((data) => {
      showNotification("Login successful! (placeholder)", "success");
      // TODO: Redirect to dashboard
    })
    .catch((error) => {
      showNotification("Login failed (placeholder)", "error");
    });
}

function showNotification(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    info: "bg-blue-500",
  };
  const toast = document.createElement("div");
  toast.className = `fixed top-5 right-5 px-4 py-2 rounded shadow text-white z-50 ${
    colors[type] || colors.info
  }`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.remove();
  }, 3000);
}

document.getElementById("forgotPasswordBtn").onclick = function () {
  document.getElementById("forgotModal").classList.remove("hidden");
};
document.getElementById("closeForgotModal").onclick = function () {
  document.getElementById("forgotModal").classList.add("hidden");
  document.getElementById("forgotMsg").textContent = "";
};
document.getElementById("forgotForm").onsubmit = function (e) {
  e.preventDefault();
  const index = document.getElementById("forgotIndex").value;
  if (!index) {
    document.getElementById("forgotMsg").textContent =
      "Please enter your index number.";
    document.getElementById("forgotMsg").className =
      "mt-4 text-center text-sm text-red-600";
    return;
  }
  document.getElementById("forgotModal").classList.add("hidden");
  document.getElementById("otpModal").classList.remove("hidden");
  document.getElementById("forgotMsg").textContent = "";
};
document.getElementById("closeOtpModal").onclick = function () {
  document.getElementById("otpModal").classList.add("hidden");
  document.getElementById("otpMsg").textContent = "";
};
document.getElementById("otpForm").onsubmit = function (e) {
  e.preventDefault();
  const otp = document.getElementById("otpInput").value;
  if (otp !== "123456") {
    document.getElementById("otpMsg").textContent =
      "Invalid OTP (demo: 123456)";
    document.getElementById("otpMsg").className =
      "mt-4 text-center text-sm text-red-600";
    return;
  }
  document.getElementById("otpMsg").textContent =
    "OTP verified! You can now reset your password.";
  document.getElementById("otpMsg").className =
    "mt-4 text-center text-sm text-green-600";
  // Here you could show a password reset form/modal
};
