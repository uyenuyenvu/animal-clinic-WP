/**
 * Pet Clinic Booking Frontend JavaScript
 */

jQuery(document).ready(function ($) {
  // Initialize date picker restrictions
  function initializeDateRestrictions() {
    var today = new Date().toISOString().split("T")[0];
    $('input[type="date"]').attr("min", today);
  }

  // Initialize form validation
  function initializeFormValidation() {
    $("form").on("submit", function (e) {
      var requiredFields = $(this).find("[required]");
      var isValid = true;

      requiredFields.each(function () {
        if (!$(this).val()) {
          $(this).addClass("error");
          isValid = false;
        } else {
          $(this).removeClass("error");
        }
      });

      if (!isValid) {
        e.preventDefault();
        alert("Vui lòng điền đầy đủ thông tin bắt buộc.");
        return false;
      }
    });

    // Remove error class on input
    $("input, select, textarea").on("input change", function () {
      $(this).removeClass("error");
    });
  }

  // Initialize tooltips
  function initializeTooltips() {
    $("[data-tooltip]").each(function () {
      $(this).attr("title", $(this).data("tooltip"));
    });
  }

  // Show loading state
  function showLoading(element) {
    $(element).addClass("loading");
    $(element).prop("disabled", true);
  }

  // Hide loading state
  function hideLoading(element) {
    $(element).removeClass("loading");
    $(element).prop("disabled", false);
  }

  // Show success message
  function showSuccess(message) {
    var successDiv = $('<div class="pcb-success">' + message + "</div>");
    $("body").append(successDiv);
    setTimeout(function () {
      successDiv.fadeOut(function () {
        $(this).remove();
      });
    }, 3000);
  }

  // Show error message
  function showError(message) {
    var errorDiv = $('<div class="pcb-error">' + message + "</div>");
    $("body").append(errorDiv);
    setTimeout(function () {
      errorDiv.fadeOut(function () {
        $(this).remove();
      });
    }, 5000);
  }

  // Format date for display
  function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString("vi-VN");
  }

  // Format time for display
  function formatTime(timeString) {
    return timeString;
  }

  // Get status text
  function getStatusText(status) {
    switch (status) {
      case "pending":
        return "Chờ xác nhận";
      case "confirmed":
        return "Đã xác nhận";
      case "completed":
        return "Đã hoàn thành";
      case "cancelled":
        return "Đã hủy";
      case "active":
        return "Hoạt động";
      case "inactive":
        return "Không hoạt động";
      default:
        return status;
    }
  }

  // Initialize all functions
  initializeDateRestrictions();
  initializeFormValidation();
  initializeTooltips();

  // Global error handler for AJAX
  $(document).ajaxError(function (event, xhr, settings, error) {
    console.error("AJAX Error:", error);
    showError("Có lỗi xảy ra. Vui lòng thử lại.");
  });

  // Add CSS for dynamic elements
  $("<style>")
    .prop("type", "text/css")
    .html(
      `
            .pcb-success {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #d4edda;
                color: #155724;
                padding: 15px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            }
            .pcb-error {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #f8d7da;
                color: #721c24;
                padding: 15px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                z-index: 9999;
                animation: slideIn 0.3s ease;
            }
            .error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 3px rgba(220,53,69,0.1) !important;
            }
            .loading {
                opacity: 0.6;
                pointer-events: none;
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `
    )
    .appendTo("head");

  // Make functions globally available
  window.pcbFrontend = {
    showLoading: showLoading,
    hideLoading: hideLoading,
    showSuccess: showSuccess,
    showError: showError,
    formatDate: formatDate,
    formatTime: formatTime,
    getStatusText: getStatusText,
  };
});
